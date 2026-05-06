document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Target Days Toggle
    const dayPills = document.querySelectorAll('.day-pill');
    dayPills.forEach(pill => {
        pill.addEventListener('click', () => {
            pill.classList.toggle('active');
        });
    });

    // 2. Header Tabs Toggle with Redirection
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            if (tab.textContent.trim() === 'Saved Routines') {
                window.location.href = 'savedRoutine.html';
                return;
            }
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
        });
    });

    // 3. Close Recommendation Card
    const closeBtn = document.querySelector('.close-btn');
    const recommendationCard = document.querySelector('.recommendation-card');
    
    if (closeBtn && recommendationCard) {
        closeBtn.addEventListener('click', () => {
            recommendationCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease, margin 0.3s ease, padding 0.3s ease, height 0.3s ease';
            recommendationCard.style.opacity = '0';
            recommendationCard.style.transform = 'scale(0.95)';
            recommendationCard.style.height = recommendationCard.offsetHeight + 'px'; 
            
            recommendationCard.offsetHeight; // Force reflow
            
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
                const newExercise = exerciseItems[0].cloneNode(true);
                
                const inputs = newExercise.querySelectorAll('input');
                inputs.forEach(input => input.value = '');
                
                const title = newExercise.querySelector('h3');
                if (title) title.textContent = 'New Exercise';
                
                const subtitle = newExercise.querySelector('p');
                if (subtitle) subtitle.textContent = 'Select muscle group';
                
                newExercise.style.opacity = '0';
                newExercise.style.transform = 'translateY(10px)';
                newExercise.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                exerciseList.appendChild(newExercise);
                
                setTimeout(() => {
                    newExercise.style.opacity = '1';
                    newExercise.style.transform = 'translateY(0)';
                }, 10);
                
                updateMovementBadge();
            }
        });
    }

    function updateMovementBadge() {
        const badge = document.querySelector('.exercise-header .badge');
        if (badge) {
            const currentCount = document.querySelectorAll('.exercise-item').length;
            badge.textContent = `${currentCount} Movements`;
        }
    }

    // --- BUTTON HANDLERS ---

    // 5. Sidebar Navigation
    const navLinks = document.querySelectorAll('.nav-links a');
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href === '#' || href === '') {
                e.preventDefault(); // Prevent default anchor behavior only if it's a dummy link
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            }
        });
    });

    // 6. Start Session Button
    const startSessionBtn = document.querySelector('.start-session-btn');
    if (startSessionBtn) {
        startSessionBtn.addEventListener('click', () => {
            const originalText = startSessionBtn.innerHTML;
            startSessionBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Starting...';
            setTimeout(() => {
                startSessionBtn.innerHTML = originalText;
                alert('Session Started! Ready to crush it.');
            }, 800);
        });
    }

    // 7. Save Draft Button (Saves to LocalStorage)
    const saveDraftBtn = document.querySelector('.btn-secondary');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', () => {
            
            // Collect Routine Data
            const routineNameInput = document.getElementById('routine-name');
            const routineName = routineNameInput && routineNameInput.value.trim() !== '' 
                                ? routineNameInput.value.trim() 
                                : 'Draft Routine';
            
            const activeDays = Array.from(document.querySelectorAll('.day-pill.active')).map(pill => pill.textContent);
            
            const exercises = [];
            document.querySelectorAll('.exercise-item').forEach(item => {
                const title = item.querySelector('h3') ? item.querySelector('h3').textContent : 'Unknown Exercise';
                const inputs = item.querySelectorAll('input');
                const sets = inputs.length > 0 ? inputs[0].value : '0';
                const reps = inputs.length > 1 ? inputs[1].value : '0';
                
                exercises.push({
                    title: title,
                    sets: sets,
                    reps: reps
                });
            });

            // Create Routine Object
            const newRoutine = {
                id: Date.now(),
                name: routineName,
                days: activeDays,
                exercises: exercises,
                dateSaved: new Date().toLocaleDateString()
            };

            // Save to Local Storage
            let savedRoutines = [];
            const existingRoutines = localStorage.getItem('savedRoutines');
            if (existingRoutines) {
                try {
                    savedRoutines = JSON.parse(existingRoutines);
                } catch(e) {}
            }
            
            savedRoutines.push(newRoutine);
            localStorage.setItem('savedRoutines', JSON.stringify(savedRoutines));

            // UI Feedback
            const originalText = saveDraftBtn.textContent;
            saveDraftBtn.textContent = 'Saving...';
            setTimeout(() => {
                saveDraftBtn.textContent = 'Saved!';
                setTimeout(() => {
                    saveDraftBtn.textContent = originalText;
                }, 2000);
            }, 500);
        });
    }

    // 8. Publish Routine Button
    const publishBtn = document.querySelector('.btn-primary');
    if (publishBtn) {
        publishBtn.addEventListener('click', () => {
            const originalHtml = publishBtn.innerHTML;
            publishBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Publishing...';
            setTimeout(() => {
                publishBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Published!';
                setTimeout(() => {
                    publishBtn.innerHTML = originalHtml;
                }, 2000);
            }, 800);
        });
    }

    // 9. Movement Library Tags
    const tags = document.querySelectorAll('.tag');
    tags.forEach(tag => {
        tag.addEventListener('click', () => {
            tag.classList.toggle('active');
            if(tag.classList.contains('active')) {
                tag.style.backgroundColor = 'var(--primary-color, #f97316)';
                tag.style.color = 'white';
                tag.style.borderColor = 'var(--primary-color, #f97316)';
            } else {
                tag.style.backgroundColor = '';
                tag.style.color = '';
                tag.style.borderColor = '';
            }
        });
    });

    // 10. View All Templates Link
    const viewAllLink = document.querySelector('.view-all');
    if (viewAllLink) {
        viewAllLink.addEventListener('click', (e) => {
            e.preventDefault();
            alert('Opening all templates library...');
        });
    }

    // 11. Template Items
    const templateItems = document.querySelectorAll('.template-item');
    templateItems.forEach(item => {
        item.style.cursor = 'pointer';
        item.addEventListener('click', () => {
            const title = item.querySelector('h4').textContent;
            alert(`Loading template: ${title}`);
        });
    });
});
