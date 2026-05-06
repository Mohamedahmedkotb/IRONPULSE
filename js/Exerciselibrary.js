document.addEventListener('DOMContentLoaded', () => {
    // 1. Prevent form submission on enter key in search bar
    const searchForm = document.querySelector('form');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
        });
    }

    // 2. Search functionality
    const searchBar = document.querySelector('.search-bar');
    const exercisesContainer = document.querySelector('.Exercises');
    
    // Dynamically get all current exercise cards so search works for newly loaded ones too
    function getExerciseCards() {
        if (!exercisesContainer) return [];
        // Get all direct div children of .Exercises
        const cards = Array.from(exercisesContainer.children).filter(child => child.tagName === 'DIV');
        return cards.map(card => {
            // Find the h2 inside the card which contains the title
            const titleEl = card.querySelector('h2');
            return {
                el: card,
                title: titleEl
            };
        });
    }

    if (searchBar) {
        searchBar.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const currentCards = getExerciseCards();
            
            currentCards.forEach(cardObj => {
                if (!cardObj.el || !cardObj.title) return;
                
                const titleText = cardObj.title.textContent.toLowerCase();
                const tags = Array.from(cardObj.el.querySelectorAll('.tag')).map(t => t.textContent.toLowerCase());
                
                const matches = titleText.includes(searchTerm) || tags.some(tag => tag.includes(searchTerm));
                
                if (matches) {
                    cardObj.el.style.display = '';
                    cardObj.el.style.opacity = '1';
                } else {
                    cardObj.el.style.display = 'none';
                    cardObj.el.style.opacity = '0';
                }
            });
        });
    }

    // 3. Filter buttons toggle
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent form submission
            btn.classList.toggle('active');
        });
    });

    // 4. More filters button
    const moreFiltersBtn = document.querySelector('.more-filters-btn');
    if (moreFiltersBtn) {
        moreFiltersBtn.addEventListener('click', (e) => {
            e.preventDefault();
            alert('Opening advanced filter panel...');
        });
    }

    // 5. Custom Exercise Button
    const customExerciseBtn = document.querySelector('.customexercise');
    if (customExerciseBtn) {
        customExerciseBtn.addEventListener('click', (e) => {
            e.preventDefault();
            alert('Opening Custom Exercise creator...');
        });
    }

    // 6. Load More Button - Dynamically inject new exercises
    const loadMoreBtn = document.querySelector('.btn-load-more');
    if (loadMoreBtn && exercisesContainer) {
        loadMoreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const originalText = loadMoreBtn.textContent;
            loadMoreBtn.textContent = 'Loading Exercises...';
            loadMoreBtn.style.opacity = '0.7';
            
            // Mock database of new exercises
            const additionalExercises = [
                {
                    title: 'Deadlift',
                    img: '../assets/media/deadlift.png',
                    tags: ['Pull', 'Compound', 'Legs', 'Back'],
                    className: 'deadlift'
                },
                {
                    title: 'Overhead Press',
                    img: '../assets/media/overhead_press.png',
                    tags: ['Push', 'Compound', 'Shoulders'],
                    className: 'overhead-press'
                },
                {
                    title: 'Lat Pulldown',
                    img: '../assets/media/lat_pulldown.jpg',
                    tags: ['Pull', 'Isolation', 'Back'],
                    className: 'lat-pulldown'
                }
            ];

            // Simulate network delay
            setTimeout(() => {
                additionalExercises.forEach(ex => {
                    const exerciseDiv = document.createElement('div');
                    exerciseDiv.className = `${ex.className} exercise-card-new`;
                    
                    let tagsHtml = '';
                    ex.tags.forEach(tag => {
                        tagsHtml += `<span class="tag-new">${tag}</span>`;
                    });

                    exerciseDiv.innerHTML = `
                        <h2 class="card-title-new">${ex.title}</h2>
                        <div class="img-container">
                            <img src="${ex.img}">
                        </div>
                        <div class="tags">
                            ${tagsHtml}
                        </div>
                    `;
                    
                    exercisesContainer.appendChild(exerciseDiv);
                    
                    // Trigger CSS animation class
                    setTimeout(() => {
                        exerciseDiv.classList.add('loaded');
                    }, 50);
                });

                loadMoreBtn.textContent = originalText;
                loadMoreBtn.style.opacity = '1';
                
                // Hide the load more button once we've loaded all our mock data
                loadMoreBtn.style.display = 'none';
            }, 1000);
        });
    }
});
