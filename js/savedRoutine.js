document.addEventListener('DOMContentLoaded', () => {
    // 5. Sidebar Navigation
    const navLinks = document.querySelectorAll('.nav-links a');
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href === '#' || href === '') {
                e.preventDefault(); 
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            }
        });
    });

    const container = document.querySelector('.saved-routines-container');
    
    let savedRoutines = [];
    const existingRoutines = localStorage.getItem('savedRoutines');
    if (existingRoutines) {
        try {
            savedRoutines = JSON.parse(existingRoutines);
        } catch(e) {
            console.error("Could not parse saved routines", e);
        }
    }

    if (savedRoutines.length === 0) {
        container.innerHTML = '<div class="no-routines">No saved routines found. Go back to "Build New" to create your first routine!</div>';
        return;
    }

    const grid = document.createElement('div');
    grid.className = 'saved-routines-grid';

    // Reverse so the newest ones show up first
    savedRoutines.reverse().forEach(routine => {
        const card = document.createElement('div');
        card.className = 'routine-card';
        
        let daysHtml = routine.days && routine.days.length > 0 
            ? `<strong style="color: #4f46e5;">Days:</strong> ${routine.days.join(', ')}` 
            : 'No specific target days';

        let exercisesHtml = '<ul class="exercise-list-mini">';
        if (routine.exercises && routine.exercises.length > 0) {
            routine.exercises.forEach(ex => {
                exercisesHtml += `<li><strong>${ex.title}</strong> - ${ex.sets} sets x ${ex.reps} reps</li>`;
            });
        } else {
            exercisesHtml += '<li>No exercises added.</li>';
        }
        exercisesHtml += '</ul>';

        card.innerHTML = `
            <h3>${routine.name}</h3>
            <p>${daysHtml} <br> <small>Saved on: ${routine.dateSaved}</small></p>
            ${exercisesHtml}
        `;
        grid.appendChild(card);
    });

    container.appendChild(grid);
});
