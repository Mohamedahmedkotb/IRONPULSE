document.addEventListener('DOMContentLoaded', () => {
    // Timer logic
    const timeDisplay = document.getElementById('elapsed-time');
    const pauseBtn = document.getElementById('pause-btn');
    const progressCircle = document.querySelector('.progress-ring__circle');
    
    let secondsElapsed = 0; // Starting at 45:22 as per mockup
    let timerInterval = null;
    let isPaused = false;
    
    // Set initial ring progress (e.g., 80% full)
    const circumference = 2 * Math.PI * 70;
    progressCircle.style.strokeDasharray = circumference;
    progressCircle.style.strokeDashoffset = circumference - (0.8 * circumference);

    function formatTime(totalSeconds) {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    function startTimer() {
        timerInterval = setInterval(() => {
            if (!isPaused) {
                secondsElapsed++;
                timeDisplay.textContent = formatTime(secondsElapsed);
                // Slightly animate the ring to show activity
                const progress = (secondsElapsed % 3600) / 3600; // Just an example progression
                const offset = circumference - (progress * circumference);
                // progressCircle.style.strokeDashoffset = offset;
            }
        }, 1000);
    }

    pauseBtn.addEventListener('click', () => {
        isPaused = !isPaused;
        const icon = pauseBtn.querySelector('i');
        if (isPaused) {
            icon.classList.remove('fa-pause');
            icon.classList.add('fa-play');
            pauseBtn.style.backgroundColor = '#e2e8f0';
        } else {
            icon.classList.remove('fa-play');
            icon.classList.add('fa-pause');
            pauseBtn.style.backgroundColor = 'var(--gray-bg)';
        }
    });

    startTimer();

    // Data for exercises based on mockup
    const exercises = [
        { id: 1, set: 1, name: 'Barbell Bench Press', weight: '135', reps: '10', status: 'completed' },
        { id: 2, set: 2, name: 'Barbell Bench Press', weight: '185', reps: '8', status: 'completed' },
        { id: 3, set: 3, name: 'Barbell Bench Press', weight: '205', reps: '6', status: 'completed' },
        { id: 4, set: 1, name: 'Incline Dumbbell Press', weight: '65', reps: '10', status: 'pending' },
        { id: 5, set: 2, name: 'Incline Dumbbell Press', weight: '--', reps: '--', status: 'pending' }
    ];

    const exerciseList = document.getElementById('exercise-list');

    function renderExercises() {
        exerciseList.innerHTML = '';
        
        exercises.forEach((ex, index) => {
            const tr = document.createElement('tr');
            
            // Add subtle animation delay for each row
            tr.style.opacity = '0';
            tr.style.animation = `fadeInUp 0.4s ease forwards ${0.5 + (index * 0.1)}s`;
            
            const isCompleted = ex.status === 'completed';
            const weightClass = ex.weight === '--' ? 'stat-input empty' : 'stat-input';
            const repsClass = ex.reps === '--' ? 'stat-input empty' : 'stat-input';

            tr.innerHTML = `
                <td><span class="set-number">${ex.set}</span></td>
                <td><span class="exercise-name">${ex.name}</span></td>
                <td class="input-cell"><input type="text" class="${weightClass}" value="${ex.weight}" /></td>
                <td class="input-cell"><input type="text" class="${repsClass}" value="${ex.reps}" /></td>
                <td class="status-cell">
                    <button class="status-btn ${isCompleted ? 'completed' : 'pending'}" data-id="${ex.id}">
                        <i class="fa-solid fa-check"></i>
                    </button>
                </td>
            `;
            exerciseList.appendChild(tr);
        });

        // Add event listeners to newly created status buttons
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const exercise = exercises.find(e => e.id === id);
                
                if (exercise) {
                    // Toggle status
                    exercise.status = exercise.status === 'completed' ? 'pending' : 'completed';
                    
                    // Update UI class
                    if (exercise.status === 'completed') {
                        this.classList.remove('pending');
                        this.classList.add('completed');
                    } else {
                        this.classList.remove('completed');
                        this.classList.add('pending');
                    }
                    
                    // Add a tiny pop animation
                    this.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                }
            });
        });

        // Add focus/blur effects for inputs
        document.querySelectorAll('.stat-input').forEach(input => {
            input.addEventListener('focus', function() {
                if (this.value === '--') {
                    this.value = '';
                    this.classList.remove('empty');
                }
            });
            
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.value = '--';
                    this.classList.add('empty');
                }
            });
        });
    }

    renderExercises();

    // Finish Workout Button Animation
    const finishBtn = document.querySelector('.finish-workout-btn');
    finishBtn.addEventListener('click', () => {
        finishBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
        finishBtn.style.opacity = '0.8';
        
        setTimeout(() => {
            finishBtn.innerHTML = '<i class="fa-solid fa-check"></i> Workout Saved';
            finishBtn.style.backgroundColor = 'var(--green-icon)';
            finishBtn.style.boxShadow = '0 4px 14px 0 rgba(34, 197, 94, 0.39)';
            
            setTimeout(() => {
                alert('Workout completed successfully!');
            }, 500);
        }, 1500);
    });
});
